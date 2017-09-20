import { Injectable } from '@angular/core';

import { JwtAuthHttp } from './http-auth.service';
import { environment } from './../../environments/environment';
import { consoleLog } from '../shared/helpers';

@Injectable()
export class AuthService {
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

      return {
        identityId: 'Lorem',
        username: 'Lorem',
      }
    });
  }

  logout() {
    consoleLog('AuthService: logout');
    localStorage.clear();
  }
}
