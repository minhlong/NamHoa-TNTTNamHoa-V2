import { Router } from '@angular/router';
import { Component, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';

import { smoothlyMenu } from '../../../helpers';
import { AppState } from '../../core/store';

declare var jQuery: any;

@Component({
  selector: 'app-topnavbar',
  templateUrl: 'topnavbar.template.html'
})
export class TopnavbarComponent implements OnDestroy {
  authSub: any;
  lopIDSub: any;
  taiKhoan: any;
  lopID: any;

  constructor(
    private router: Router,
    private store: Store<AppState>
  ) {
    this.authSub = this.store.select((state: AppState) => state.auth.tai_khoan).subscribe(res => {
      this.taiKhoan = res;
    });
    this.lopIDSub = this.store.select((state: AppState) => state.auth.lop_hoc_hien_tai_id).subscribe(res => {
      this.lopID = res;
    });
  }

  toggleNavigation(): void {
    jQuery('body').toggleClass('mini-navbar');
    smoothlyMenu();
  }

  ngOnDestroy() {
    this.authSub.unsubscribe();
    this.lopIDSub.unsubscribe();
  }
}
