import { Routes } from '@angular/router';

import { BasicComponent } from './modules/layouts/basic.component';
import { BlankComponent } from './modules/layouts/blank.component';

import { AuthGuard } from './services/guards/auth-guard.service';
import { GuestGuard } from './services/guards/guest-guard.service';

import { LogoutComponent } from './components/logout.component';
import { LoginComponent } from './components/login/login.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';

const noAuth: Routes = [
  {
    path: '', component: BlankComponent,
    children: [
      { path: 'dang-nhap', component: LoginComponent, canActivate: [GuestGuard] },
    ],
  },
];

const hasAuth: Routes = [
  {
    path: '', component: BasicComponent,
    children: [
      { path: '', redirectTo: 'trang-chu', pathMatch: 'full' },
      { path: 'trang-chu', component: DashboardComponent },
      { path: 'dang-xuat', component: LogoutComponent },
    ],
    canActivateChild: [AuthGuard]
  },
];

export const ROUTES: Routes = [
  // Main redirect
  { path: '', redirectTo: 'trang-chu', pathMatch: 'full' },

  // App views
  ...noAuth,
  ...hasAuth,

  // Handle all other routes
  { path: '**', redirectTo: 'trang-chu', pathMatch: 'full' }
];
