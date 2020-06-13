import { AuthService } from './services/auth.service';
import { AuthEffect } from './store/effects/auth.effect';
import { environment } from '@env/environment';
import { NgModule } from '@angular/core';
import { StoreDevtoolsModule } from '@ngrx/store-devtools';
import { CommonModule } from '@angular/common';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { JwtModule } from '@auth0/angular-jwt';
import { HttpTokenInterceptor } from './interceptors/http.token.interceptor';

import { reducer } from './store';

import {
  ApiService,
  AuthGuard,
  JwtService,
  UserService,
  GuestGuard
} from './services';
import { StoreModule } from '@ngrx/store';
import { EffectsModule } from '@ngrx/effects';

@NgModule({
  imports: [
    StoreModule.forRoot(reducer),
    EffectsModule.forRoot([AuthEffect]),
    StoreDevtoolsModule.instrument({ logOnly: environment.production }),

    CommonModule,

    HttpClientModule,
    JwtModule.forRoot({
      config: {
        tokenGetter: () => {
          return localStorage.getItem('access_token');
        },
        whitelistedDomains: ['tnttnamhoa.org'],
        // blacklistedRoutes: ['http://example.com/examplebadroute/'],
      },
    }),
  ],
  providers: [
    { provide: HTTP_INTERCEPTORS, useClass: HttpTokenInterceptor, multi: true },
    ApiService,

    AuthGuard,
    GuestGuard,

    AuthService,
    JwtService,
    UserService
  ],
  declarations: []
})
export class CoreModule { }
