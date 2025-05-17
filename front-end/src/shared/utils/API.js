export default class API {
  constructor() {
    this.baseUrl = "/API/v1"
  }

  static init() {
    if (!API.instance) {
      API.instance = new API();
    }
    return API.instance;
  }

  post({
    url,
    data,
    callback,
    callbackError = null,
    forceText = false,
    forceBlob = false,
  }) {

    const formBody = data;

    let self = this;

    fetch(url, {
      method: 'POST',
      body: formBody,
      cache: "no-store",
      headers: {
        "Authorization": "Bearer " + self.oauthToken,
        "Content-type": "application/json; charset=UTF-8",
      }
    })
      .then(response => {

      })
      .catch((error) => {
        console.log('Error: ', error);
        callbackError && callbackError(error);
      });

  }
}