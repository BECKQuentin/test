import axios from "axios";
// const emitter = require('tiny-emitter/instance')

axios.defaults.headers.common['X-From-Axios'] = true

axios.interceptors.response.use((response) => {
  // if (response.data?.flashMessage) {
  //   emitter.emit('flash-message', response.data.flashMessage)
  // }
  return response
}, (error) => {
  console.log(error)
  // const {message, response} = error
  // emitter.emit('flash-message', {
  //   type: 'error',
  //   message: response?.data?.detail ?? message,
  //   duration: 5000,
  // })
  return Promise.reject(error);
});

export default axios
