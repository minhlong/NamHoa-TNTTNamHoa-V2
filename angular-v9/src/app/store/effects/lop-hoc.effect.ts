import { ToasterService } from 'angular2-toaster';
import { Injectable } from '@angular/core';
import { Effect, Actions } from '@ngrx/effects';
import { Observable } from 'rxjs';
import { Router } from '@angular/router';

import * as LopHocAction from '../actions/lop-hoc.action';
import { environment } from 'src/environments/environment';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class LopHocEffect {
  urlAPI = environment.apiURL + '/lop-hoc';

  constructor(
    private toasterService: ToasterService,
    private http: HttpClient,
    private actions$: Actions,
    private router: Router,
  ) { }

  // @Effect() getInfo$ = this.actions$.ofType(LopHocAction.GETINFO)
  //   .switchMap((payload: any) => {
  //     return this._http.get(this.urlAPI + '/' + payload.id)
  //       .map(res => res.json())
  //       .map(res => {
  //         return new LopHocAction.GetLopInfoSucc(res);
  //       });
  //   }).catch((err) => {
  //     this.toasterService.pop('error', 'Lỗi!', err);
  //     return Observable.of(new LopHocAction.Err(err));
  //   });
}
