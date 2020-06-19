<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="nav-small-cap pb-0 text-center">CRM OTUCHAT</li>
                <li class="nav-devider"></li>
                <li>
                    <a href="/home"><i class="mdi mdi-desktop-mac"></i>Dashboard</a>
                </li>
                @if (Session::get('sub_menu'))
                    @foreach (Session::get('menu') as $menu)
                        <li>
                            <a class="has-arrow " href="#" aria-expanded="false"><i class="{{ $menu->deskripsi }}"></i><span
                                class="hide-menu">{{ $menu->nama_menu }}</span></a>
                            @foreach (Session::get('sub_menu') as $sub_menu_member)
                                @if($sub_menu_member->id_menu == $menu->id)
                                    <ul aria-expanded="false" class="collapse">
                                        <li>
                                                <a href="{{ $sub_menu_member->endpoint_sub_menu }}">{{ $sub_menu_member->nama_submenu }}</a>
                                        </li>
                                    </ul>
                                @endif
                            @endforeach
                        </li>
                    @endforeach
                @endif
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>