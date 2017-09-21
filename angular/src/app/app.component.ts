import { Component, AfterViewInit } from '@angular/core';
import { correctHeight, detectBody, consoleLog } from './shared/helpers';
import { Store } from '@ngrx/store';
import { ToasterConfig } from 'angular2-toaster';

import { AppState } from './store/reducers/index';
import * as AuthAction from './store/actions/auth.action';


declare var jQuery: any;

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
})
export class AppComponent implements AfterViewInit {
  public toasterconfig: ToasterConfig = new ToasterConfig({
    timeout: 5000,
    positionClass: 'toast-bottom-left',
    newestOnTop: false,
    mouseoverTimerStop: true,
  });

  constructor(
    private store: Store<AppState>
  ) {
    consoleLog('AppComponent: constructor');
    this.store.dispatch(new AuthAction.ValidateToken());
  }

  ngAfterViewInit() {
    // Run correctHeight function on load and resize window event
    jQuery(window).bind('load resize', function () {
      correctHeight();
      detectBody();
    });

    // Correct height of wrapper after metisMenu animation.
    jQuery('.metismenu a').click(() => {
      setTimeout(() => {
        correctHeight();
      }, 300)
    });
  }
}
