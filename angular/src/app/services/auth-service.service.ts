import { Injectable } from '@angular/core';
import { JwtHelper, tokenNotExpired } from 'angular2-jwt';

import { JwtAuthHttp } from './http-auth.service';
import { environment } from './../../environments/environment';
import { consoleLog } from '../shared/helpers';

@Injectable()
export class AuthService {
  private jwtHelper: JwtHelper = new JwtHelper();
  private urlAPI = environment.apiURL + '/dang-nhap';

  constructor(
    private _http: JwtAuthHttp,
  ) {
  }

  /**
   * Authticate base on username/email and password
   *
   * @param id
   * @param password
   */
  authenticate(id: string, password: string) {
    consoleLog('AuthService: authenticate');

    return this._http.post(this.urlAPI, {
      'id': id,
      'password': password
    }).map(res => res.json()).map(res => {
      localStorage.setItem('token', res.data)

      return this.jwtHelper.decodeToken(res.data);
    });
  }

  isAuthenticated() {
    console.log(tokenNotExpired());
    if (!tokenNotExpired()) {
      return false;
    }

    return this.jwtHelper.decodeToken(localStorage.getItem('token'));
  }

  logout() {
    consoleLog('AuthService: logout');
    localStorage.clear();
  }
}
