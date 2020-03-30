import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { AppState } from '../../../store/reducers';
import * as LopHocAction from '../../../store/actions/lop-hoc.action';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss']
})
export class ChiTietComponent implements OnDestroy {

  isLoading = true;
  lopHocInfo: any = {};

  sub$: any;
  subLH$: any;

  constructor(
    private router: Router,
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private activatedRoute: ActivatedRoute
  ) {
    this.sub$ = this.activatedRoute.params.subscribe(params => {
      this.store.dispatch(new LopHocAction.GetLopInfo(params['id']));
    })

    this.subLH$ = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
      this.lopHocInfo = res;
      this.isLoading = false;
    });
  }

  ngOnDestroy() {
    this.sub$.unsubscribe();
    this.subLH$.unsubscribe();
  }

  activeRoute(routename: string): boolean {
    return this.router.url.indexOf(routename) > -1;
  }
}
