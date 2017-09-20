import { Component } from '@angular/core';
import { JwtAuthHttp } from '../../services/http-auth.service';
import { consoleLog } from '../../shared/helpers';

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.component.html',
  styleUrls: ['./dashboard.component.scss'],
})

export class DashboardComponent {

  constructor(
    private _http: JwtAuthHttp,
  ) {
    consoleLog('DashboardComponent: constructor');
  }
}
