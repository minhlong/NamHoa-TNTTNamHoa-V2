import { Component, OnInit, Input, Output, EventEmitter, OnDestroy } from '@angular/core';

@Component({
  selector: 'app-phieu-lien-lac',
  templateUrl: './phieu-lien-lac.component.html',
  styleUrls: ['./phieu-lien-lac.component.scss']
})
export class PhieuLienLacComponent implements OnInit {
  @Input() apiData;
  @Input() thieuNhiArr;
  @Output() updateInfo = new EventEmitter();

  pagingTN = {
    id: 'tnTable',
    itemsPerPage: 2,
    currentPage: 1,
  }

  constructor() { }

  ngOnInit() {
    console.log(this.thieuNhiArr);
  }

  cancel() {
    this.updateInfo.emit(null);
  }

  print() {
    window.print();
  }
}
