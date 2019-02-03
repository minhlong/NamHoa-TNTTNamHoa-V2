import { Injectable, Pipe } from '@angular/core';
import { Observable, Subject, Subscription, pipe } from 'rxjs';
import { tap, map } from 'rxjs/operators';
import { JwtAuthHttp } from '../../services/http-auth.service';

declare global {
  interface Date {
    addDays(days): Date;
    toISO(): string;
  }
}

Date.prototype.addDays = function (days) {
  const date = new Date(this.valueOf());
  date.setDate(date.getDate() + days);
  return date;
}

Date.prototype.toISO = function () {
  const date = new Date(this.valueOf());
  const year: any = date.getFullYear();
  let month: any = date.getMonth() + 1;
  let dt: any = date.getDate();

  if (dt < 10) {
    dt = '0' + dt;
  }
  if (month < 10) {
    month = '0' + month;
  }

  return year + '-' + month + '-' + dt;
}

@Injectable()
export class DataService {
  private commonPipe = pipe(
    // Generate sundays array
    map((res: any) => {
      let min = null;
      let max = null;

      res.forEach(item => {
        if (!min || min > item.ngay_muon) {
          min = item.ngay_muon
        }
        if (!min || min > item.ngay_tra) {
          min = item.ngay_tra
        }

        if (!max || max < item.ngay_muon) {
          max = item.ngay_muon
        }
        if (!max || max < item.ngay_tra) {
          max = item.ngay_tra
        }
      });

      const dateArr = this.getSundays(min, max);
      return [res, dateArr];
    }),
  );

  constructor(
    private _http: JwtAuthHttp,
  ) {
  }

  private generateData(): any[] {
    const data = [];
    data.push({
      id: 11,
      ten: 'Thiet Bi x',
      ngay_muon: '2019-01-03',
      ngay_tra: '2019-01-13',
      tai_khoan_id: 'HT028',
      ten_tai_khoan: 'Giuse Testing',
      trang_thai: 'DA_TRA',
    })

    data.push({
      id: 12,
      ten: 'Thiet Bi x1',
      ngay_muon: '2019-01-05',
      ngay_tra: '2019-01-27',
      tai_khoan_id: 'HT028',
      ten_tai_khoan: 'Giuse Hồ Minh Long',
      trang_thai: 'DA_TRA',
    })

    for (let _i = 0; _i < 10; _i++) {
      data.push({
        id: _i + 1,
        ten: 'Thiet Bi ' + (_i + 1),
        ngay_muon: '2019-01-15',
        ngay_tra: '2019-02-10',
        tai_khoan_id: 'HT028',
        ten_tai_khoan: 'Giuse Hồ Minh Long',
        trang_thai: 'DANG_MUON',
        ghi_chu: 'Lorem',
      })
    }
    return data;
  }

  delete(id): Observable<any> {
    const sampleData = this.generateData().filter(c => c.id !== id);
    return Observable.create(observer => {
      observer.next(sampleData);
      observer.complete();
    }).let(this.commonPipe);
  }

  addNew(item): Observable<any> {
    item.trang_thai = 'DA_TRA';
    const sampleData = this.generateData();
    sampleData.push(item)
    return Observable.create(observer => {
      observer.next(sampleData);
      observer.complete();
    }).let(this.commonPipe);
  }

  updateList(dataArrr: any[]): Observable<any[]> {
    return Observable.create(observer => {
      const sampleData = this.generateData();
      observer.next(sampleData);
      observer.complete();
    }).let(this.commonPipe);
  }

  getList(): Observable<any> {
    return Observable.create(observer => {
      const sampleData = this.generateData();
      observer.next(sampleData);
      observer.complete();
    }).let(this.commonPipe);
  }

  private getSundays(startDate, stopDate) {
    const dateArray = new Array();
    let currentDate = new Date(startDate);
    const stop = new Date(stopDate);
    while (currentDate <= stop) {
      // Only add sunday
      if (currentDate.getDay() === 0) {
        dateArray.push(currentDate.toISO());
      }
      currentDate = currentDate.addDays(1);
    }
    return dateArray;
  }
}
