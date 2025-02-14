
import IndexMain from './../components/layouts/IndexMain'
import RoomsPage from './../components/views/pages/Hotel/Rooms/RoomsPage'
import CategoriesPage from './../components/views/pages/Hotel/Categories/CategoriesPage'
import ZonasPage from './../components/views/pages/Hotel/Zonas/ZonasPage'
import BookingsPage from './../components/views/pages/Hotel/Bookings/BookingsPage'
import BookingDetail from './../components/views/pages/Hotel/Bookings/BookingDetail'
import ReceptionPage from './../components/views/pages/Hotel/Reception/ReceptionPage'
import BranchsHotelPage from './../components/views/pages/Hotel/Reception/BranchsPage'

import CheckInPage from './../components/views/pages/Hotel/Reception/CheckIn'
import CheckOutPage from './../components/views/pages/Hotel/Reception/CheckOut'
import CheckInReservation from './../components/views/pages/Hotel/Reception/CheckInReservation'

import RoomsSalesPage from './../components/views/pages/Hotel/Pos/RoomsPage'
import RoomSalePage from './../components/views/pages/Hotel/Pos/PosPage'


export default 
[
    {
        path: '/rooms',
        name: 'Rooms',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: RoomsPage,
        },
   },
   {
        path: '/type_room',
        name: 'Categories',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: CategoriesPage,
        },
   },
   {
        path: '/zones_hotel',
        name: 'Zonas',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: ZonasPage,
        },
   },
   {
        path: '/bookings',
        name: 'Bookings',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: BookingsPage,
        },
   },
   {
        path: '/booking/:id',
        name: 'BookingDetail',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: BookingDetail,
        },
   },
   {
        path: '/reception/:id',
        name: 'Reception',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: ReceptionPage,
        },
   },
   {
        path: '/checkin/:id',
        name: 'CheckIn',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: CheckInPage,
        },
   },
   {
        path: '/checkin_booking/:id',
        name: 'CheckInReservation',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: CheckInReservation,
        },
   },
   {
        path: '/checkout/:id',
        name: 'CheckOut',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: CheckOutPage,
        },
   },
   {
        path: '/rooms_sales/',
        name: 'RoomsSalesPage',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: RoomsSalesPage,
        },
   },
   {
        path: '/room_pos/:id',
        name: 'RoomSalePage',
        meta: { requiresAuth: true },
        components: {
             default: IndexMain,
             MainView: RoomSalePage,
        },
   },
   {
     path: "/branchHotelSystem/",
     name: "BranchsHotelPage",
     meta: { requiresAuth: true },
     components: {
         default: IndexMain,
         MainView: BranchsHotelPage
     }
  },
]