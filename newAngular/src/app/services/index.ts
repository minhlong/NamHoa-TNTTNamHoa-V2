// import { Store } from '@ngrx/store';
// import { Http } from '@angular/http';

// import { GuestGuard } from './guards/guest-guard.service';
// import { GuestGuard } from './guards/guest-guard.service';
// import { AuthGuard } from './guards/auth-guard.service';
// import { AuthService } from './auth-service.service';
// import { JwtAuthHttp, authFactory } from './http-auth.service';
// import { VersionHandlerService } from './version-handler.service';

// export function providers() {
//   return [
//     // AuthService,
//     AuthGuard,
//     GuestGuard,
//     // {
//     //   provide: JwtAuthHttp,
//     //   useFactory: authFactory,
//     //   deps: [Http,
//     //     Store]
//     // },
//     // VersionHandlerService,
//   ];
// }

export * from './guards/guest-guard.service';
export * from './guards/auth-guard.service';
