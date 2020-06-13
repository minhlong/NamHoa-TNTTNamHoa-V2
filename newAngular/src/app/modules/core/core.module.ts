import { AuthService } from './services/auth.service';
import { AuthEffect } from './store/effects/auth.effect';
import { environment } from '@env/environment';
import { NgModule } from '@angular/core';
import { StoreDevtoolsModule } from '@ngrx/store-devtools';
import { HttpClientModule } from '@angular/common/http';
import { JwtModule } from '@auth0/angular-jwt';

import { reducer } from './store';

import {
  ApiService,
  AuthGuard,
  // UserService,
  GuestGuard
} from './services';
import { StoreModule } from '@ngrx/store';
import { EffectsModule } from '@ngrx/effects';

@NgModule({
  imports: [
    StoreModule.forRoot(reducer),
    EffectsModule.forRoot([AuthEffect]),
    StoreDevtoolsModule.instrument({ logOnly: environment.production }),

    HttpClientModule,
    JwtModule.forRoot({
      config: {
        tokenGetter: () => {
          console.log(111);
          return localStorage.getItem('token');
        },
        whitelistedDomains: [
          'api.tnttnamhoa.org',
        ],
      },
    }),
  ],
  providers: [
    ApiService,

    AuthGuard,
    GuestGuard,

    AuthService,
    // JwtService,
    // UserService
  ],
  declarations: []
})
export class CoreModule { }
