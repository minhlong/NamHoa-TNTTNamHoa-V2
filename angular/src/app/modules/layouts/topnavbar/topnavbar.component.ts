import { Component } from '@angular/core';
import { Store } from '@ngrx/store';

import { smoothlyMenu } from '../../../_helpers';
import { AppState } from '../../../store/reducers/index';
import { Observable } from 'rxjs/Rx';

declare var jQuery: any;

@Component({
  selector: 'app-topnavbar',
  templateUrl: 'topnavbar.template.html'
})
export class TopnavbarComponent {
  taiKhoan: Observable<any>;

  constructor(
    private store: Store<AppState>
  ) {
    this.taiKhoan = this.store.select((state: AppState) => state.auth.tai_khoan);
  }

  toggleNavigation(): void {
    jQuery('body').toggleClass('mini-navbar');
    smoothlyMenu();
  }
}
