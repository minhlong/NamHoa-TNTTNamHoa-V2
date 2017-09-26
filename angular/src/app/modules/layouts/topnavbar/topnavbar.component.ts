import { Router } from '@angular/router';
import { Component, OnDestroy } from '@angular/core';
import { Store } from '@ngrx/store';

import { smoothlyMenu } from '../../../_helpers';
import { AppState } from '../../../store/reducers/index';
import { Observable } from 'rxjs/Rx';

declare var jQuery: any;

@Component({
  selector: 'app-topnavbar',
  templateUrl: 'topnavbar.template.html'
})
export class TopnavbarComponent implements OnDestroy {
  authSub: any;
  taiKhoan: any = {};

  constructor(
    private router: Router,
    private store: Store<AppState>
  ) {
    this.authSub = this.store.select((state: AppState) => state.auth.tai_khoan).subscribe(res => {
      this.taiKhoan = res;
    });
  }

  toggleNavigation(): void {
    jQuery('body').toggleClass('mini-navbar');
    smoothlyMenu();
  }

  view() {
    this.router.navigate(['/tai-khoan/chi-tiet/', this.taiKhoan.id]);
  }

  ngOnDestroy() {
    this.authSub.unsubscribe();
  }
}
