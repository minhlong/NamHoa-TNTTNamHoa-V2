import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LayoutsModule } from './modules/layouts/layouts.module';
import { SharedModule } from './modules/sharedx/shared.module';
import { LoginComponent } from './login/login.component';
import { TrangChuComponent } from './trang-chu/trang-chu.component';
import { CoreModule } from './modules/core';
// import { AuthGuard, GuestGuard } from './services';


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    TrangChuComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,

    // SharedModule,
    CoreModule,

    // Layout
    LayoutsModule,
  ],
  providers: [
    // AuthGuard,
    // GuestGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
