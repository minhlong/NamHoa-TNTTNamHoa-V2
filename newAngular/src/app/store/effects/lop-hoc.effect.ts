import { ToasterService } from 'angular2-toaster';
import { Injectable } from '@angular/core';
import { Effect, Actions } from '@ngrx/effects';
import { Observable } from 'rxjs';
import { Router } from '@angular/router';

import * as LopHocAction from '../actions/lop-hoc.action';
import { JwtAuthHttp } from '../../services/http-auth.service';
import { environment } from 'environments/environment';

@Injectable()
export class LopHocEffect {
  urlAPI = environment.apiURL + '/lop-hoc';

  constructor(
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
    private actions$: Actions,
    private router: Router,
  ) { }

  @Effect() getInfo$ = this.actions$.ofType(LopHocAction.GETINFO)
    .switchMap((payload: any) => {
      return this._http.get(this.urlAPI + '/' + payload.id)
        .map(res => res.json())
        .map(res => {
          return new LopHocAction.GetLopInfoSucc(res);
        });
    }).catch((err) => {
      this.toasterService.pop('error', 'Lỗi!', err);
      return Observable.of(new LopHocAction.Err(err));
    });
}
