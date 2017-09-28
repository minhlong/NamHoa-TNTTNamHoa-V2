import { ToasterService } from 'angular2-toaster';
import { Component, OnInit, ViewChild } from '@angular/core';
import { Headers, RequestOptions, URLSearchParams } from '@angular/http';

import { JwtAuthHttp } from '../../../services/http-auth.service';
import { environment } from '../../../../environments/environment';

@Component({
  selector: 'app-tao-moi',
  templateUrl: './tao-moi.component.html',
  styleUrls: ['./tao-moi.component.scss']
})
export class TaoMoiComponent implements OnInit {
  @ViewChild('fileInput') fileInput;

  private urlAPI = environment.apiURL + '/tai-khoan/tap-tin';
  active: number
  current = 1
  totalStep = 3
  isLoading = false;
  dataArr: any;

  constructor(
    private toasterService: ToasterService,
    private _http: JwtAuthHttp,
  ) {
    this.active = this.current
  }

  ngOnInit() {
  }

  addFile(): void {
    const fi = this.fileInput.nativeElement;
    if (fi.files && fi.files[0]) {
      const formData = new FormData();
      formData.append('file', fi.files[0]);

      this.isLoading = true;
      this._http.post(this.urlAPI, formData).map(res => res.json()).subscribe(res => {
        this.dataArr = res.data;
        this.nextStep();
        this.isLoading = false;
      }, _err => {
        if (typeof _err === 'string') {
          this.toasterService.pop('error', 'Lỗi!', _err);
        } else {
          for (const _field in _err) {
            if (_err.hasOwnProperty(_field)) {
              _err[_field].forEach(_mess => {
                this.toasterService.pop('error', 'Lỗi!', _mess);
              });
            }
          }
        }
        this.isLoading = false;
      });
    } else {
      this.toasterService.pop('error', 'Lỗi!', 'Chưa chọn tập tin.');
    }
  }

  taoTaiKhoan() {
    this.isLoading = true;
    this._http.post(this.urlAPI + '/tao', { data: this.dataArr }).map(res => res.json()).subscribe(res => {
      this.nextStep();
      this.isLoading = false;
    }, _err => {
      if (typeof _err === 'string') {
        this.toasterService.pop('error', 'Lỗi!', _err);
      } else {
        for (const _field in _err) {
          if (_err.hasOwnProperty(_field)) {
            _err[_field].forEach(_mess => {
              this.toasterService.pop('error', 'Lỗi!', _mess);
            });
          }
        }
      }
      this.isLoading = false;
    });
  }

  prevStep() {
    this.current--;
  }

  nextStep() {
    this.current++;
    if (this.current > this.active) {
      this.active = this.current
    }
  }

  restart() {
    this.current = 1;
    this.active = this.current;
  }

  getActiveStep(step) {
    let _cls = '';
    if (this.active >= step) {
      _cls += ' active-step';

      if (this.current > step) {
        _cls += ' step-done';
      } else if (this.current === step) {
        _cls += ' editable-step';
      }
    }

    return _cls;
  }
}
