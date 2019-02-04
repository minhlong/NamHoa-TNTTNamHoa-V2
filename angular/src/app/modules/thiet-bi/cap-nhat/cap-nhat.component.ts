import { Component, OnInit, OnDestroy } from '@angular/core';
import { Observable, Subject, Subscription } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { Store } from '@ngrx/store';
import { Router } from '@angular/router';

import { DataService } from '../data.service';
import { AppState } from '../../../store/reducers/index';
import { AuthState } from '../../../store/reducers/auth.reducer';

declare let jQuery: any;

@Component({
  selector: 'app-cap-nhat',
  templateUrl: './cap-nhat.component.html',
  styleUrls: ['./cap-nhat.component.scss']
})
export class CapNhatComponent implements OnDestroy {
  sub$: Subscription;
  subAuth$: Subscription;
  loadData$ = new Subject<any>();
  search$ = new Subject<any>();
  changFilter$ = new Subject<any>();

  isLoading = true;
  filterTT = '';
  curAuth: AuthState;
  thietBiArr = []
  ngayArr = []

  pagingTN = {
    id: 'thietbi-form-Table',
    itemsPerPage: 10,
    currentPage: 1,
  }

  maskOption = {
    mask: [/[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, '-', /[0-9]/, /[0-9]/, /[0-9]/, /[0-9]/],
    guide: true,
  }

  constructor(
    private store: Store<AppState>,
    private router: Router,
    private dataServ: DataService,
  ) {
    this.subAuth$ = this.store.select((state: AppState) => state.auth).subscribe(res => {
      this.curAuth = res;
    });

    setTimeout(() => {
      // Trigger Search
      this.dataServ.getList().subscribe(res => {
        this.loadData$.next(res);
      }).unsubscribe();
      this.search$.next(null);
      this.changFilter$.next(this.filterTT);
    }, 0);

    this.sub$ = this.search$.debounceTime(400)
      .pipe(
        tap(c => this.isLoading = true),
      )
      .combineLatest(
        this.changFilter$,
        this.loadData$.pipe(
          map(([res, ngayArr]) => {
            const data = res.map(c => {
              c.ngay_muon = this.convertDay(c.ngay_muon);
              c.ngay_tra = this.convertDay(c.ngay_tra);
              return c;
            });
            return data;
          }),
        ),
      ).pipe(
        // Map for search string
        map(([searchStr, filterStatus, thietbiArr]) => {
          let data = thietbiArr;
          if (searchStr) {
            data = thietbiArr.filter(item => {
              return item.ten.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1 ||
                item.ten_tai_khoan.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1 ||
                item.ngay_muon.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1 ||
                item.ngay_tra.toLowerCase().indexOf(searchStr.toLowerCase()) !== -1;
            })
          }
          return [searchStr, filterStatus, data];
        }),
        // Map for filter trang thai
        map(([searchStr, filterStatus, thietbiArr]) => {
          let data = thietbiArr;
          if (filterStatus) {
            data = thietbiArr.filter(item => {
              return item.trang_thai === filterStatus;
            })
          }
          return [searchStr, filterStatus, data];
        }),
        // Map
        map(([searchStr, filterStatus, thietBiArr]) => thietBiArr),
      ).subscribe((thietBiArr) => {
        this.isLoading = false;
        this.thietBiArr = thietBiArr;
      })
  }

  ngOnDestroy() {
    this.subAuth$.unsubscribe();
    this.sub$.unsubscribe();
    this.search$.complete();
    this.loadData$.complete();
    this.changFilter$.complete();
  }

  save() {
    this.isLoading = true;
    // this.dataServ.updateList(this.thietBiArr).subscribe(res => {
    //   this.loadData$.next(res);
    // }).unsubscribe();
    // this.router.navigate(['/thiet-bi']);
  }

  convertDay(day) {
    return day.replace(/(.+)[-|\/](.+)[-|\/](.+)/i, '$3-$2-$1');
  }
}
