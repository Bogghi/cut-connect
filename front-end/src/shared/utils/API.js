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

    let self = this;
    let headers = {
      "Content-type": "application/json; charset=UTF-8",
    };

    if(this.hasOwnProperty("oauthToke")) {
      headers["Authorization"] = "Bearer " + this.oauthToken;
    }

    fetch(url, {
      method: 'POST',
      body: JSON.stringify(data),
      cache: "no-store",
      headers: headers
    })
      .then(response => {
        callback(response);
      })
      .catch((error) => {
        console.log('Error: ', error);
        callbackError && callbackError(error);
      });

  }

  login({username, password, callback}) {

    const data = {
      username: username,
      password: password
    }
    API.init().post({
      url: this.baseUrl + "/login",
      data: data,
      callback: callback
    });
  }
}