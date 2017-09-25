import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';

@Component({
  selector: 'app-form',
  templateUrl: './form.component.html',
  styleUrls: ['./form.component.scss']
})
export class FormComponent implements OnInit {
  @Input() taiKhoanInfo: any;
  @Output() updateInfo = new EventEmitter();

  isLoading: boolean;

  constructor() { }

  ngOnInit() {
  }

  update() {
    this.updateInfo.emit('message');
  }
}
