import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from 'environments/environment';

@Injectable()
export class VersionHandlerService {

  constructor(
    private http: HttpClient,
  ) { }

  /**
     * Checks in every set frequency the version of frontend application
     */
  public initVersionCheck() {
    setInterval(() => {
      this.checkVersion();
    }, environment.versionFrequency);
  }

  /**
   * Will do the call and check if the hash has changed or not
   * @param url
   */
  private checkVersion() {
    // timestamp these requests to invalidate caches
    this.http.get(environment.versionCheckURL + '?t=' + new Date().getTime())
      .subscribe((res: { version: string }) => {
        if (res.version !== environment.version) {
          location.reload();
        }
      }, (err) => {
        console.error(err, 'Could not get version');
      });
  }

}
