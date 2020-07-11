import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent, LoginComponent, LogoutComponent } from './components';

import { CoreModule, SharedModule, LayoutsModule } from './modules';
import { TrangChuComponent } from './trang-chu/trang-chu.component';

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
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
