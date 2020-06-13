import { map } from 'rxjs/operators';
import { Injectable } from '@angular/core';

import { ApiService } from './api.service';

@Injectable()
export class AuthService {
  private url = '/dang-nhap';

  constructor(
    private apiSer: ApiService,
  ) { }

  /**
   * Authticate base on username/email and password
   */
  authenticate(id: string, password: string) {
    return this.apiSer.post(this.url, {
      id,
      password
    }).pipe(
      map((res: any) => res.json()),
      map((res: any) => {
        localStorage.setItem('token', res.data);
        return true;
        // return this.jwtHelper.decodeToken(res.data);
      })
    );
  }

  isAuthenticated() {
    // if (!tokenNotExpired()) {
    //   return false;
    // }
    return true;
    // return this.jwtHelper.decodeToken(localStorage.getItem('token'));
  }

  logout() {
    localStorage.clear();
  }
}
