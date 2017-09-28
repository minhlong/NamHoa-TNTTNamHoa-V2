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

  private urlAPI = environment.apiURL + '/upload';

  constructor(
    private _http: JwtAuthHttp,
  ) { }

  ngOnInit() {
  }

  addFile(): void {
    const fi = this.fileInput.nativeElement;
    if (fi.files && fi.files[0]) {
      const formData = new FormData();
      formData.append('file', fi.files[0]);

      this._http.post(this.urlAPI, formData).map(res => res.json()).subscribe(res => {
        console.log(res);
      }, _err => {
        console.log(_err);
      });
    }
  }
}
