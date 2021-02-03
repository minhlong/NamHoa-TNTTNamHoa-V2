import { JwtHelperService } from '@auth0/angular-jwt';
import { map } from 'rxjs/operators';
import { Injectable } from '@angular/core';

import { ApiService } from './api.service';

@Injectable()
export class AuthService {
  constructor(
    private apiSer: ApiService,
    private jwtHelper: JwtHelperService
  ) { }

  /**
   * Authticate base on username/email and password
   */
  authenticate(id: string, password: string) {
    let url = '/auth/login';
    return this.apiSer.post(url, {
      id,
      password
    }).pipe(
      map((res: any) => {
        localStorage.setItem('token', res.data);
        return this.jwtHelper.decodeToken(res.data);
      })
    );
  }

  isAuthenticated() {
    if (this.jwtHelper.isTokenExpired()) {
      return false;
    }

    return this.jwtHelper.decodeToken(localStorage.getItem('token'));
  }
}
