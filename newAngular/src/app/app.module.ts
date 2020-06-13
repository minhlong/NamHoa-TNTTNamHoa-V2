import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { LayoutsModule } from './modules/layouts/layouts.module';
import { SharedModule } from './modules/shared/shared.module';
import { TrangChuComponent } from './trang-chu/trang-chu.component';
import { CoreModule } from './modules/core';
import { LoginComponent, LogoutComponent } from './components';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    LogoutComponent,
    TrangChuComponent,
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,

    SharedModule,
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
