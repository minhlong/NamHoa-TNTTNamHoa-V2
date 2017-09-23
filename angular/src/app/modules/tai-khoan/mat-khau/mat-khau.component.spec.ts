import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MatKhauComponent } from './mat-khau.component';

describe('MatKhauComponent', () => {
  let component: MatKhauComponent;
  let fixture: ComponentFixture<MatKhauComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MatKhauComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MatKhauComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
