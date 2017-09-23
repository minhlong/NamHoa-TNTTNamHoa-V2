import { Component, AfterViewInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { Observable } from 'rxjs/Rx';
import { AppState } from '../../../store/reducers/index';

declare var jQuery: any;

@Component({
  selector: 'app-navigation',
  templateUrl: 'navigation.template.html',
  styleUrls: ['navigation.component.scss']
})

export class NavigationComponent implements AfterViewInit, OnDestroy {
  sub: any;
  phanQuyen: any = {};

  constructor(
    private router: Router,
    private store: Store<AppState>

  ) {
    this.sub = this.store.select((state: AppState) => state.auth.phan_quyen).subscribe(res => {
      res.forEach(el => {
        this.phanQuyen[el] = true;
      })
    });
  }

  ngAfterViewInit() {
    jQuery('#side-menu').metisMenu();
  }

  activeRoute(routename: string): boolean {
    return this.router.url.indexOf(routename) > -1;
  }

  activeRouteAbsolute(routename: string): boolean {
    return this.router.url === routename;
  }

  ngOnDestroy() {
    this.sub.unsubscribe();
  }
}
