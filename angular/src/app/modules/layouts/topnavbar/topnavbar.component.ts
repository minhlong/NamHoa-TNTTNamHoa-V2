import { Component } from '@angular/core';
import { Store } from '@ngrx/store';

import { smoothlyMenu } from '../../../shared/helpers';
import { AppState } from '../../../store/reducers/index';
import { Observable } from 'rxjs/Rx';

declare var jQuery: any;

@Component({
  selector: 'app-topnavbar',
  templateUrl: 'topnavbar.template.html'
})
export class TopnavbarComponent {
  username: string;

  constructor(
    private store: Store<AppState>
  ) {
    this.username = localStorage.getItem('username');
  }

  toggleNavigation(): void {
    jQuery('body').toggleClass('mini-navbar');
    smoothlyMenu();
  }
}
