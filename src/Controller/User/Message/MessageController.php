<?php

namespace App\Controller\User\Message;

use App\Entity\Chat;
use App\Entity\User;
use App\Exception\AppException;
use App\Manager\ChatMessageManager;
use App\Repository\ChatMessageRepository;
use App\Repository\ChatRepository;
use App\Security\Voter\AccountVoter;
use App\Security\Voter\ChatMessageVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;


class MessageController extends AbstractController
{
    public function __construct(
        private ChatRepository          $chatRepository,
        private ChatMessageRepository   $chatMessageRepository,
        private ChatMessageManager      $chatMessageManager,
        private SerializerInterface     $serializer,
        private TranslatorInterface     $translator,
    ){}

    /**
     * @throws AppException
     */
    #[Route('/talk-to/{user}', name: 'user_messages_talk_to')]
    #[IsGranted(AccountVoter::READ, subject:'user')]
    public function redirectToChat(User $user, Request $request): RedirectResponse
    {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if(!$chat = $this->chatRepository->findChatBetweenUsers($currentUser, $user)) {
            $chat = $this->chatMessageManager->createConversation($currentUser, $user);
        }

        return $this->redirectToRoute('user_messages', ['chat' => $chat->getId()]);
    }

    #[Route('/message/index/{chat}', name: 'user_messages', defaults: ['chat' => null])]
    #[IsGranted(ChatMessageVoter::READ, subject:'chat')]
    public function index(?Chat $chat): Response
    {

        /** @var User $user */
        $user = $this->getUser();
        $isParticipant = true;

        //Renvoyer la liste de premières conversations
        $arrChat = $this->chatRepository->findChatListForUser($user);
        $jsonChatList = $this->serializer->serialize($arrChat, 'json', ['groups' => 'chat_list']);

//        dd($jsonChatList);


        if ($chat) {
            $jsonChatRequest = $this->serializer->serialize($chat, 'json', ['groups' => 'chat_list']);
            $isParticipant = $this->chatMessageManager->hasParticipant($chat, $user);
        }

        return $this->render('user/message/index.html.twig', [
            'user'              => $user,
            'isAdminCheckin'    => json_encode(!$isParticipant && $this->isGranted('ROLE_ADMIN')),
            'jsonChatList'      => $jsonChatList,
            'jsonChatRequest'   => $jsonChatRequest ?? null,
        ]);
    }

    //Renvoi la liste des conversations
    #[Route('/message/list/{offset}', name: 'user_messages_list', options: ['expose' => true])]
    public function loadConversation(int $offset = 0): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $chatList = $this->chatRepository->findChatListForUser($user, $offset);

        // Renvoyer les résultats au format JSON
        return $this->json($chatList, context: ['groups' => 'chat_message', 'locale' => $user->getLocale(),]);
    }

    //Renvoi la liste des messages d'une conversation
    #[Route('/message/chat/{id}/{offset}', name: 'user_messages_chat', options: ['expose' => true])]
    #[IsGranted(ChatMessageVoter::READ, subject:'chat')]
    public function loadMessage(Chat $chat,int $offset = 0): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $this->chatMessageManager->readMessage($chat, $user);

        $chatMessages = $this->chatMessageRepository->findMessageByChatId($chat->getId(), $offset);

        // Renvoyer les résultats au format JSON
        return $this->json($chatMessages, context: ['groups' => 'chat_message']);
    }

    #[Route('/message/send/{id}', name: 'user_messages_send', options: ['expose' => true], methods: 'POST')]
    #[IsGranted(ChatMessageVoter::WRITE, subject:'chat')]
    public function sendMessage(Chat $chat, Request $request, ValidatorInterface $validator): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        // Récupération des données envoyées par le formulaire
        $data = json_decode($request->getContent(), true);
        $messageContent = $data['message'];

        $message = $this->chatMessageManager->createMessage($chat, $user, $messageContent);

        return $this->json($message, context: ['groups' => 'chat_message']);
    }

    #[Route('/message/polling/{id}', name: 'user_messages_polling', options: ['expose' => true], defaults: ['id' => null])]
    #[IsGranted(ChatMessageVoter::READ, subject:'chat')]
    public function pollingChat(?Chat $chat): JsonResponse
    {

        /** @var User $user */
        $user = $this->getUser();

        if ($chat) { //Permet de vérifier si un tchat est actif en front
            $newMessage = [];
            if ($chat->getLastMessageSentBy() === $user && $chat->isIsLastMessageSeen()) { //Si le dernier message est envoyé par moi et que le dernier message a été vu par l'autre
                $newMessage = $this->chatMessageRepository->findMessageByChatId($chat->getId());
            }
            $notSeenMessage = $this->chatMessageRepository->findLastMessageNoSeenForUser($chat, $user);

            $jsonNewMessage = $this->serializer->serialize(array_merge($newMessage, $notSeenMessage), 'json', ['groups' => 'chat_message']);
        }

        $jsonNotSeenChat = $this->serializer->serialize($this->chatRepository->findLastChatNoSeenByUser($user), 'json', ['groups' => 'chat_list']);

        if ($chat && $this->chatMessageManager->hasParticipant($chat, $user)) { //Empêche la lecture de l'Admin de mettre les messages à vu
            $this->chatMessageManager->readMessage($chat, $user);
        }

        $responseData = [
            'chat'          => $jsonNotSeenChat,
            'newMessage'    => $jsonNewMessage ?? [],
        ];

        return new JsonResponse($responseData);
    }

    #[Route('/message/report/{id}', name: 'user_messages_report', options: ['expose' => true])]
    #[IsGranted(ChatMessageVoter::READ, subject:'chat')]
    public function reportChat(Chat $chat): JsonResponse
    {

        /** @var User $user */
        $user = $this->getUser();

//        $email = (new Email())
//            ->setTemplateId($this->emailMailjetTemplateRepository->getTemplateId(EmailMailjetTemplate::TEMPLATE_REPORT_MESSAGE_USER, $user->getLocale()))
//            ->setContext([
//                'linkChat'  => $this->generateUrl('user_messages', ['chat' => $chat->getId()],UrlGeneratorInterface::ABSOLUTE_URL),
//                'reporter'  => $user->getFullName(),
//                'reported'  => $user === $chat->getUser1() ? $chat->getUser2()->getFullName() :$chat->getUser1()->getFullName(),
//                'reportedAt'=> (new \DateTime('NOW'))->format('Y-m-d H:i:s'),
//            ])
//            ->setSendToAdmin(true)
//        ;
//
//        $this->emailService->sendEmail($email);

        return $this->json($this->translator->trans('page.back.user.message.flash.success.chat_report'), Response::HTTP_ACCEPTED);

    }

}