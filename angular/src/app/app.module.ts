import { RouterModule } from '@angular/router';
import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpModule } from '@angular/http';
import { FormsModule } from '@angular/forms';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { EffectsModule } from '@ngrx/effects';
import { StoreModule, Store } from '@ngrx/store';
import { StoreDevtoolsModule } from '@ngrx/store-devtools'; // Have to remove on production mod
import { NgxPaginationModule } from 'ngx-pagination';
import { ToasterModule } from 'angular2-toaster';
import { TextMaskModule } from 'angular2-text-mask';

import { ROUTES } from './app.routes';

// Modules
import { LayoutsModule } from './modules/layouts/layouts.module';
import { SharedModule } from './modules/shared/shared.module';

// Services
import { providers } from './services/index';

// Redux - Effects
import { AuthEffect } from './store/effects/auth.effect';
import { LopHocEffect } from './store/effects/lop-hoc.effect';
// Redux - Reducer
import { reducer } from './store/reducers/index';

// Components
import { AppComponent } from './app.component';
import { LoginComponent } from './components/login/login.component';
import { LogoutComponent } from './components/logout.component';
import { TrangChuComponent } from './components/trang-chu/trang-chu.component';

// Pipe
import { environment } from '../environments/environment';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    LogoutComponent,
    TrangChuComponent,
  ],
  imports: [
    // Angular modules
    BrowserModule,
    HttpModule,
    FormsModule,
    BrowserAnimationsModule,
    NgxPaginationModule,
    ToasterModule,
    TextMaskModule,
    SharedModule,

    // Layout
    LayoutsModule,
    // Routes
    RouterModule.forRoot(ROUTES, { useHash: true }),

    // Redux
    StoreModule.forRoot(reducer),
    EffectsModule.forRoot([AuthEffect, LopHocEffect]),

    // Should be removed when deploy
    !environment.production ? StoreDevtoolsModule.instrument() : [],
  ],
  providers: [
    providers(), // Services
    // actions(), // Redux - Action
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
