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

  post(params)
  {
    this.fetchWrapper({
      method: "POST",
      ...params
    })
  }

  get(params)
  {
    this.fetchWrapper({
      method: "GET",
      ...params
    })
  }

  fetchWrapper({
    method,
    url,
    data,
    callback,
    callbackError = null,
  })
  {

    let headers = {
      "Content-type": "application/json; charset=UTF-8",
    };

    if(localStorage.getItem("jwt_token") !== null) {
      headers["Authorization"] = "Bearer " + localStorage.getItem("jwt_token");
    }

    const fetchOptions = {
      method: method,
      cache: "no-store",
      headers: headers
    }
    if(method === "POST" && data) {
      fetchOptions.body = JSON.stringify(data);
    }
    fetch(url, fetchOptions)
      .then(response => {

        if(response.status === 200 || response.status === 400) {
          response.json().then(data => {
            callback(data);
          })
        }
        else if(response.status === 401) {

          if(localStorage.getItem("refresh_token") !== null) {

            fetch(this.baseUrl+'/refresh', {
              method: 'GET',
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

                    const retryBody = {
                      url: url,
                      callback: callback,
                      callbackError: callbackError
                    };

                    if(method === "POST") {
                      retryBody.data = data;
                      this.post(retryBody);
                    }
                    else{
                      this.get(retryBody)
                    }
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
    this.post({
      url: this.baseUrl + "/login",
      data: data,
      callback: callback
    });
  }

  loadUserInfo({callback}) {

    this.get({
      url: this.baseUrl + "/users/get",
      callback: callback
    });

  }

  addReservation({reservation, callback}) {
    this.post({
      url: this.baseUrl + "/reservation/add",
      data: reservation,
      callback: callback
    });
  }

  deleteReservation({reservationId, callback}) {
    this.post({
      url: this.baseUrl + "/reservation/delete",
      data: {
        reservation_id: reservationId,
      },
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
    this.post({
      url: this.baseUrl + "/reservations/get",
      data: data,
      callback: callback
    });

  }

  updateReservation({reservation, callback}) {

    this.post({
      url: this.baseUrl + "/reservation/update",
      data: reservation,
      callback: callback
    });

  }

  getServices({callback}) {
    this.get({
      url: this.baseUrl + "/services/get",
      callback: callback
    });
  }

  addService({service, callback}) {
    this.post({
      url: this.baseUrl + "/service/add",
      data: service,
      callback: callback
    });
  }

  updateService({service, callback}) {
    this.post({
      url: this.baseUrl + "/service/update",
      data: service,
      callback: callback
    });
  }

  deleteService({serviceId, callback}) {
    this.post({
      url: this.baseUrl + "/service/delete",
      data: {
        service_id: serviceId,
      },
      callback: callback
    });
  }

}