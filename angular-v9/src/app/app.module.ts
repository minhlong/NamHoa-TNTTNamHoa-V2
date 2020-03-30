import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { HttpClientModule } from '@angular/common/http';
import { StoreModule } from '@ngrx/store';
import { EffectsModule } from '@ngrx/effects';
import { JwtModule } from '@auth0/angular-jwt';
import { StoreDevtoolsModule } from '@ngrx/store-devtools'; // Have to remove on production mod
import { ToasterModule, ToasterService } from 'angular2-toaster';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import { AppRoutingModule } from './app-routing.module';
import { LayoutsModule } from './modules/layouts/layouts.module';

import { AppComponent } from './app.component';
import { LoginComponent } from './components/login/login.component';

// Services
import { providers } from './services';

import { reducer } from './store/reducers';
import { AuthEffect } from './store/effects/auth.effect';
import { LopHocEffect } from './store/effects/lop-hoc.effect';
import { environment } from 'src/environments/environment';
import { SharedModule } from './modules/shared/shared.module';
import { NgxPaginationModule } from 'ngx-pagination';
// import { SelectModule } from 'ng2-select';
import { LogoutComponent } from './components/logout.component';
import { TrangChuComponent } from './components/trang-chu/trang-chu.component';
import { TextMaskModule } from 'angular2-text-mask';

export function tokenGetter() {
  return localStorage.getItem('token');
}

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    LogoutComponent,
    TrangChuComponent
  ],
  imports: [
    BrowserModule,
    HttpClientModule,
    FormsModule,
    BrowserAnimationsModule,
    NgxPaginationModule,
    ToasterModule,
    // SelectModule,
    TextMaskModule,
    SharedModule,
    JwtModule.forRoot({
      config: {
        tokenGetter,
        whitelistedDomains: ['new-api.tnttnamhoa.org'],
      }
    }),

    // Layout
    LayoutsModule,
    AppRoutingModule,

    // Redux
    StoreModule.forRoot(reducer),
    EffectsModule.forRoot([AuthEffect, LopHocEffect]),

    // Should be removed when deploy
    !environment.production ? StoreDevtoolsModule.instrument() : []
  ],
  providers: [
    providers(), // Services
    ToasterService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
