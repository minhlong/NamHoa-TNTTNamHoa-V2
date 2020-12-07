import { ComponentFixture, TestBed, waitForAsync } from '@angular/core/testing';

import { TrangChuComponent } from './trang-chu.component';

describe('TrangChuComponent', () => {
  let component: TrangChuComponent;
  let fixture: ComponentFixture<TrangChuComponent>;

  beforeEach(waitForAsync(() => {
    TestBed.configureTestingModule({
      declarations: [ TrangChuComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TrangChuComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
