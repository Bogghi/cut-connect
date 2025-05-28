import router from "@/console/router/index.js";

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
  }) {

    let headers = {
      "Content-type": "application/json; charset=UTF-8",
    };

    if(localStorage.getItem("jwt_token") !== null) {
      headers["Authorization"] = "Bearer " + localStorage.getItem("jwt_token");
    }

    fetch(url, {
      method: 'POST',
      body: JSON.stringify(data),
      cache: "no-store",
      headers: headers
    })
      .then(response => {

        if(response.status === 200 || response.status === 400) {
          response.json().then(data => {
            callback(data);
          })
        }
        else if(response.status === 401) {

          if(localStorage.getItem("refresh_token") !== null) {

            fetch(this.baseUrl+'/refresh', {
              method: 'POST',
              cache: 'no-store',
              headers: {
                "Content-type": "application/json; charset=UTF-8",
                "Authorization": "Bearer " + localStorage.getItem("refresh_token")
              }
            })
              .then(response => {
                response.json().then(freshData => {

                  if(freshData.status === "OK") {
                    localStorage.removeItem("jwt_token");
                    localStorage.setItem("jwt_token", freshData.token);

                    this.post({
                      url: url,
                      data: data,
                      callback: callback,
                      callbackError: callbackError
                    });
                  }

                });
              });

          }
          else {
            localStorage.removeItem("jwt_token");
            localStorage.removeItem("refresh_token");
            router.push("/console/login");
          }

        }
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

  addReservation({reservation, callback}) {
    API.init().post({
      url: this.baseUrl + "/reservation/add",
      data: reservation,
      callback: callback
    });
  }

  getReservations({window_type, start, end, callback}) {

    let data = {
      'window_type': window_type,
      'start': start,
    };
    if(end) {
      data['end'] = end;
    }
    API.init().post({
      url: this.baseUrl + "/reservations/get",
      data: data,
      callback: callback
    });
  }
}