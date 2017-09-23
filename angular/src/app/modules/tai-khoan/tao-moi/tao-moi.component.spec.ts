import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TaoMoiComponent } from './tao-moi.component';

describe('TaoMoiComponent', () => {
  let component: TaoMoiComponent;
  let fixture: ComponentFixture<TaoMoiComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TaoMoiComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TaoMoiComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
