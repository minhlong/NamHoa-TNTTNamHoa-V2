import { Component, AfterViewInit } from '@angular/core';
import { correctHeight, detectBody } from './_helpers';
import { Store } from '@ngrx/store';
import { ToasterConfig } from 'angular2-toaster';

import { AppState } from './store/reducers';
import * as AuthAction from './store/actions/auth.action';
import { VersionHandlerService } from './services/version-handler.service';

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
    private versionCheck: VersionHandlerService,
    private store: Store<AppState>
  ) {
    this.store.dispatch(new AuthAction.ValidateToken());
    this.versionCheck.initVersionCheck();
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
