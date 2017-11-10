import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';

@Component({
  selector: 'app-phieu-lien-lac',
  templateUrl: './phieu-lien-lac.component.html',
  styleUrls: ['./phieu-lien-lac.component.scss']
})
export class PhieuLienLacComponent implements OnInit {
  @Output() updateInfo = new EventEmitter();

  constructor() { }

  ngOnInit() {
  }

  cancel() {
    this.updateInfo.emit(null);
  }
}
