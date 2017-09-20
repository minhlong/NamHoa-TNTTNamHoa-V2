import { Store } from '@ngrx/store';
import { Http } from '@angular/http';

// import { AuthActions } from '../store/actions/auth.action';

import { GuestGuard } from './guards/guest-guard.service';
import { AuthGuard } from './guards/auth-guard.service';
import { AuthService } from './auth-service.service';
import { JwtAuthHttp, authFactory } from './http-auth.service';

export function providers() {
  return [
    AuthService,
    AuthGuard,
    GuestGuard,
    {
      provide: JwtAuthHttp,
      useFactory: authFactory,
      deps: [Http,
        // AuthActions,
        Store]
    }
  ];
}
