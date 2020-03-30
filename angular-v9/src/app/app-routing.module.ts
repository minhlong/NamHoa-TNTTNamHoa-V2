import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { BlankComponent } from './modules/layouts/blank.component';
import { LoginComponent } from './components/login/login.component';
import { GuestGuard } from './services/guards/guest-guard.service';
import { BasicComponent } from './modules/layouts/basic.component';
import { LogoutComponent } from './components/logout.component';
import { TrangChuComponent } from './components/trang-chu/trang-chu.component';
import { AuthGuard } from './services/guards/auth-guard.service';

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
      { path: 'dang-xuat', component: LogoutComponent },
      { path: 'trang-chu', component: TrangChuComponent },
      {
        path: 'tai-khoan',
        loadChildren: () => import('./modules/tai-khoan/tai-khoan.module').then(m => m.TaiKhoanModule)
        // loadChildren: './modules/tai-khoan/tai-khoan.module#TaiKhoanModule' 
      },
      // { path: 'lop-hoc', loadChildren: './modules/lop-hoc/lop-hoc.module#LopHocModule' },
      // { path: 'thiet-bi', loadChildren: './modules/thiet-bi/thiet-bi.module#ThietBiModule' },
      // { path: 'thu-moi', loadChildren: './modules/thu-moi/thu-moi.module#ThuMoiModule' },
      // { path: 'khoa-hoc', loadChildren: './modules/khoa-hoc/khoa-hoc.module#KhoaHocModule' },
      // { path: 'phan-quyen', loadChildren: './modules/phan-quyen/phan-quyen.module#PhanQuyenModule' },
      // { path: 'phan-nhom', loadChildren: './modules/phan-nhom/phan-nhom.module#PhanNhomModule' },
    ],
    canActivateChild: [AuthGuard]
  },
];

const routes: Routes = [
  // Main redirect
  { path: '', redirectTo: 'trang-chu', pathMatch: 'full' },

  // App views
  ...noAuth,
  ...hasAuth,

  // Handle all other routes
  { path: '**', redirectTo: 'trang-chu', pathMatch: 'full' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes, { useHash: true })],
  exports: [RouterModule]
})
export class AppRoutingModule { }
