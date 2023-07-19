<script>
export default {
  template: `#template_edit_role`,
  delimiters: ['${', '}'],
  props: {
    initUser: {type: String},
  },
  data() {
    return {
      user: JSON.parse(this.initUser),
      isUpdatedUserLoading: false,
      updatedUserError: '',
    }
  },
  mounted() {
  },
  methods: {
    getFullName(user) {
      const firstName = user.firstname.charAt(0).toUpperCase() + user.firstname.slice(1);
      const lastName = user.lastname.toUpperCase();
      return `${firstName} ${lastName}`;
    },
    sendRequest(url) {
      this.isUpdatedUserLoading = true;
      this.$http.post(url, this.user)
          .then((response) => {
            this.user = JSON.parse(response.data);
            this.isUpdatedUserLoading = false;
          })
          .catch((error) => {
            if (error.response) {
              this.updatedUserError = error.response.data; // Utiliser error.response.data pour afficher votre propre message d'erreur
            } else {
              this.updatedUserError = error;
            }
          })
          .finally(() => {
            this.isUpdatedUserLoading = false;
          });
    },

  },
}
</script>