  <div class="user-profile-minimal-wrapper">
      <div class="user-profile-minimal" id="userProfileMinimalHeader" tabindex="0" role="button" aria-haspopup="true"
          aria-expanded="false">

          @auth
              <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" class="user-avatar-header">
              {{-- <span class="user-name-header">{{ Auth::user()->name }}</span> --}}
          @endauth

          @guest
              <img src="{{ asset('images/defaults/user.png') }}" alt="User Name" class="user-avatar-header">
          @endguest
      </div>
      <div class="header-dropdown user-avatar-dropdown" id="userAvatarDropdownHeader">
          <ul>
              @auth
                  <li>
                      <a href="{{ route('dashboard.profile') }}">
                          <i class="fas fa-user-circle"></i>
                          {{ __('app/layouts.header.profile') }}
                      </a>
                  </li>
                  <li>
                      <button id="logoutButton">
                          <i class="fas fa-sign-out-alt"></i>
                          {{-- Or your preferred icon --}}
                          {{ __('app/layouts.header.logout') }}
                          {{-- Make sure 'messages.logout' is in your lang files --}}
                      </button>
                  </li>
              @endauth
              @guest
                  <li>
                      <button type="button" id="openAuthDialogBtn">
                          <i class="fas fa-sign-out-alt"></i>
                          {{ __('app/layouts.header.login') }}
                      </button>
                  </li>
                  {{-- <li><a href="#"><i class="fas fa-cog"></i>
                            Account Settings
                        </a></li> --}}
              @endguest

          </ul>
      </div>
  </div>
