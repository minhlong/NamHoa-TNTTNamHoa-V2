import { AuthService } from './auth-service.service';
import { VersionHandlerService } from './version-handler.service';
import { AuthGuard } from './guards/auth-guard.service';
import { GuestGuard } from './guards/guest-guard.service';

export function providers() {
  return [
    AuthService,
    AuthGuard,
    GuestGuard,
    VersionHandlerService,
  ];
}
