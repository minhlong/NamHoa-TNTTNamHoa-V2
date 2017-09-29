import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';

import { AppState } from './../../../store/reducers/index';
import * as LopHocAction from './../../../store/actions/lop-hoc.action';

@Component({
  selector: 'app-chi-tiet',
  templateUrl: './chi-tiet.component.html',
  styleUrls: ['./chi-tiet.component.scss']
})
export class ChiTietComponent implements OnDestroy {

  isLoading = true;
  lopHocID: string;
  lopHocInfo: any = {};

  parSub: any;
  lhSub: any;

  constructor(
    private store: Store<AppState>,
    private toasterService: ToasterService,
    private activatedRoute: ActivatedRoute
  ) {
    this.parSub = this.activatedRoute.params.subscribe(params => {
      this.lopHocID = params['id'];
      this.store.dispatch(new LopHocAction.GetInfo(this.lopHocID));
    })

    this.lhSub = this.store.select((state: AppState) => state.lop_hoc.thong_tin).subscribe(res => {
      this.lopHocInfo = res;
      this.isLoading = false;
    });
  }

  ngOnDestroy() {
    this.parSub.unsubscribe();
    this.lhSub.unsubscribe();
  }
}
