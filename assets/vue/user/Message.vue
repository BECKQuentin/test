<script>
export default {
  template: `#template_message`,
  delimiters: ['${', '}'],
  props: {
    initLocale: {type: String},
    initCurrentUserId: {type: String},
    initIsAdminCheckin: {type: String},
    initChatList: {type: String},
    initChatRequest: {type: String},
  },
  data() {
    return {
      locale: this.initLocale,
      currentUserId: JSON.parse(this.initCurrentUserId),
      isAdminCheckin:  JSON.parse(this.initIsAdminCheckin),
      chatList: JSON.parse(this.initChatList),
      chatMessage: [],
      chatRequest: this.initChatRequest,
      activeChat: null,
      messageContent: '',
      isConversationLoading: false,
      isMessageLoading: false,
      isOlderMessageLoading : false,
      isMoreConversation: true, //Empêche rechargement au scroll si pas de nouvelle conversation
      isMoreMessage: true, //Empêche rechargement au scroll si pas de nouveau message
      inBoxDisabled: false,
      ellipsisDisabled: true,
      alertError: '',
      alertSuccess: '',
    }
  },
  mounted() {
    this.startPolling();
    document.addEventListener('click', this.closeEllipsis);

    if (this.chatRequest) { //Si un chat est demandé alors on l'affiche
      this.activeChat = JSON.parse(this.initChatRequest);
      this.loadMessage(this.activeChat.id)
    }
  },

  computed: {

    setActiveChat() {
      return this.chatList.find(chat => chat.id === this.activeChat?.id)
    },

    groupedMessages() { // Groupe les messages par utilisateur

      if (!this.chatMessage) return

      // Tri des messages par ordre décroissant de la propriété createdAt
      const sortedMessages = this.chatMessage.slice().sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt));

      const groups = [];
      let currentGroup = [];

      for (let i = 0; i < sortedMessages.length; i++) {
        if (i === 0 || sortedMessages[i].sender.id !== sortedMessages[i - 1].sender.id) {
          if (currentGroup.length > 0) {
            groups.push(currentGroup);
          }
          currentGroup = [sortedMessages[i]];
        } else {
          currentGroup.push(sortedMessages[i]);
        }
      }

      if (currentGroup.length > 0) {
        groups.push(currentGroup);
      }

      return groups;
    },
  },
  methods: {

    /**
     * Renvoi les conversations avec message non lus et si conversation active alors renvoi les nouveaux messages
     */
    startPolling() {
      setInterval(() => {

        this.$http.get(this.$path('user_messages_polling', {id: this.activeChat?.id}))
            .then(({data}) => {

              if (data.chat) {
                let notSeenChat = JSON.parse(data.chat);

                if (notSeenChat.length > 0) {
                  notSeenChat.forEach(chat => {
                    let index = this.chatList.findIndex(c => c.id === chat.id);
                    if (index !== -1) {
                      this.chatList.splice(index, 1, chat); //Remplace la nouvelle conversation
                    } else {
                      this.chatList.push(chat); //Ajoute la nouvelle conversation
                    }
                  });
                }
              }

              if (data.newMessage) {
                let newMessage = JSON.parse(data.newMessage);
                if (newMessage.length > 0  && this.activeChat) {
                  console.log('yes')
                  this.replaceOrAddNewMessage(newMessage);
                  this.scrollBottomMessageContentBody();
                }
              }
            })

      },15000)
    },
    onScrollBotConversation({target: {scrollTop, clientHeight, scrollHeight}}) {
      if ((scrollTop + clientHeight + 50) >= scrollHeight && !this.isConversationLoading && this.isMoreConversation === true) { //50 est la valeur supplémentaire être sûr d'atteindre le bas
        this.loadConversation()
      }
    },
    onScrollTopMessage({target: {scrollTop, clientHeight, scrollHeight}}) {
      if (scrollTop === 0 && !this.isMessageLoading && this.isMoreMessage === true) {
        this.loadOlderMessage(this.chatMessage[0]?.chat.id)
      }
    },
    scrollBottomMessageContentBody() {
      this.$nextTick(() => { //Attend que le DOM soit mis à jour
        const messageContentBody = this.$refs.messageContentBody;
        messageContentBody.scrollTop = messageContentBody.scrollHeight;
      });
    },
    replaceOrAddNewMessage(arrMessages) {
      console.log(arrMessages)
      arrMessages.forEach(message => {
        if (this.activeChat.id === message.chat.id) { // Ajoute les messages seulement si le Chat du message est actif
          let index = this.chatMessage.findIndex(c => c.id === message.id); //Vérifie si doit remplacer ou ajouter le message
          if (index !== -1) {
            this.chatMessage.splice(index, 1, message); //Remplace le message
          } else {
            this.chatMessage.push(message); //Ajoute le nouveau message
          }
        }
      })
    },
    canDisplayUnreadMessage(chat) { //Empêche l'affichage du badge si page active et user qui n'a pas envoyé le dernier message
      if (this.activeChat) {
        if (this.activeChat.id === chat.id && chat.lastMessageSentBy?.id !== this.currentUserId) {
          return false;
        }
      }

      // if (!chat.isLastMessageSeen && this.currentUserId !== chat.lastMessageSentBy.id && chat.unreadMessagesCount > 0) {
      //   return true;
      // }
      return false;
    },
    getUnreadMessageCount(chat) {
      return chat.chatMessage?.filter(message => message.readAt === null).length;
    },
    changeConversation(id) {
      this.isAdminCheckin = false; //Enlever ça, car chargement de conversation depuis sa propre liste de message
      this.toggleInBox();

      this.activeChat = this.chatList.find(chat => chat.id === id); //Ajout de la nouvelle conversation active
      this.isMoreMessage = true; //Peut-être de nouveau message, car changement de conversation
      this.chatMessage?.splice(0, this.chatMessage?.length); // Effacer les messages précédents
      this.loadMessage(id);
    },
    loadConversation() {
      this.isConversationLoading = true;
      const url = this.$path('user_messages_list', {'offset': this.chatList.length});
      this.$http.get(url)
          .then(({data}) => {
            this.chatList.push(...data);
            data.length < 100 ? this.isMoreConversation = false : null; //Empêche rechargement au scroll si pas de nouvelle conversation
          })
          .finally(() => {
            this.isConversationLoading = false;
          })
    },
    loadMessage(chatId) {

      this.isMessageLoading = true;

      let params = {'id': this.activeChat.id};
      if (this.chatMessage) {
        params.offset = this.chatMessage.length;
      }
      this.$http.get(this.$path('user_messages_chat', params))
          .then(({data}) => {
            if (this.activeChat.id === chatId) { //Si pas changé de conversation,

              this.replaceOrAddNewMessage(data);

              data.length < 100 ? this.isMoreMessage = false : null; //Empêche nouvelle requête au scroll, car pas de nouveau message
              this.isMessageLoading = false;
            }
          })
          .finally(() => {
            this.scrollBottomMessageContentBody();
          })
    },

    loadOlderMessage(chatId) {
      if (this.isOlderMessageLoading) {
        return;
      }

      this.isOlderMessageLoading = true;
      const url = this.$path('user_messages_chat', {'id': chatId, 'offset': this.chatMessage.length});
      this.$http.get(url)
          .then(({data}) => {
            if (this.activeChat.id === chatId) {
              this.chatMessage.push(...data);
              data.length < 100 ? this.isMoreMessage = false : null;
            }
          })
          .finally(() => {
            this.isOlderMessageLoading = false;
          });
    },

    sendMessage() {
      const url = this.$path('user_messages_send', {id: this.activeChat.id});

      const messageContent = this.messageContent;
      this.messageContent = '';

      this.$http.post(url, {message: messageContent})
          .then(({data}) => {
            this.chatMessage.push(data)
          })
          .finally(() => this.scrollBottomMessageContentBody())
    },

    reportChat(chat, confirmMessage) {
      if (confirm(confirmMessage)) {
        const url = this.$path('user_messages_report', {'id': chat.id});
        this.$http.get(url)
            .then(({data}) => {
              this.alertSuccess = data;
            })
      }
    },

    toggleInBox() {
      this.inBoxDisabled = !this.inBoxDisabled;

      const inBoxBtn = document.querySelector('.inBox__toggle-btn');
      inBoxBtn.style.transform = this.inBoxDisabled ? 'rotate(0deg)' : 'rotate(180deg)';
    },

    toggleEllipsis(event) {
      this.ellipsisDisabled = !this.ellipsisDisabled;
    },
    closeEllipsis() {
      if ((!event.target.closest('.ellipsis'))) {
        this.ellipsisDisabled = true;
      }
    }

  }
}
</script>