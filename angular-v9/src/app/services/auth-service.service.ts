import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

import { environment } from 'src/environments/environment';
import { map } from 'rxjs/operators';
import { JwtHelperService } from '@auth0/angular-jwt';

@Injectable()
export class AuthService {
  private urlAPI = environment.apiURL + '/dang-nhap';
  private jwtHelper = new JwtHelperService();

  constructor(
    private http: HttpClient,
  ) {
  }

  /**
   * Authticate base on username/email and password
   *
   * @param id id
   * @param password password
   */
  authenticate(id: string, password: string) {
    return this.http.post(this.urlAPI, { id, password }).pipe(
      map((res: any) => {
        localStorage.setItem('token', res.data);
        return this.jwtHelper.decodeToken(res.data);
      }),
    );
  }

  isAuthenticated() {
    const token: string = localStorage.getItem('token');
    if (this.jwtHelper.isTokenExpired(token)) {
      return false;
    }

    return this.jwtHelper.decodeToken(localStorage.getItem('token'));
  }

  logout() {
    localStorage.clear();
  }
}
