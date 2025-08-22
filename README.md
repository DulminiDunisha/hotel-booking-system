# Hotel Booking System

A modern, full-featured hotel booking system built with Laravel, Vue.js, and Inertia.js. This system provides a complete solution for hotel management with both guest-facing and administrative interfaces.

## 🚀 Features

### Guest Features
- **Room Browsing**: Browse available rooms with detailed information
- **Advanced Filtering**: Filter rooms by type, price, capacity, and amenities
- **Room Details**: View comprehensive room information with images and amenities
- **Booking System**: Complete booking flow with date selection and guest information
- **Booking Confirmation**: Detailed confirmation page with booking details
- **Emergency Contact**: Emergency request form for guests
- **Gallery**: Hotel image gallery
- **Responsive Design**: Mobile-friendly interface

### Administrative Features
- **Dashboard**: Real-time statistics and overview
- **Booking Management**: View, edit, and manage all bookings
- **Room Management**: Add, edit, and manage room inventory
- **User Management**: Manage guest and staff accounts
- **Payment Hub**: Track and manage payments
- **Emergency Cases**: Handle emergency requests
- **Gallery Management**: Upload and manage hotel images
- **Analytics**: Occupancy rates, revenue tracking, and booking statistics

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x
- **Frontend**: Vue.js 3.x with TypeScript
- **Styling**: Tailwind CSS 4.x
- **Database**: MySQL/PostgreSQL
- **Authentication**: Laravel Breeze with Inertia
- **UI Components**: Custom components with Reka UI
- **Icons**: Lucide Vue Next
- **Charts**: Placeholder for future chart integration

## 📋 Requirements

- PHP 8.2 or higher
- Node.js 18 or higher
- Composer
- MySQL 8.0 or PostgreSQL 13
- Web server (Apache/Nginx)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hotel-booking-system
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hotel_booking
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start development servers**
   ```bash
   # Terminal 1: Start Laravel server
   php artisan serve
   
   # Terminal 2: Start Vite development server
   npm run dev
   ```

## 🗄️ Database Structure

### Core Tables
- **users**: Guest and admin accounts
- **rooms**: Room inventory and details
- **bookings**: Booking records
- **payments**: Payment transactions
- **room_availabilities**: Room availability schedules
- **room_seasonal_rates**: Seasonal pricing
- **emergency_cases**: Emergency requests
- **notifications**: System notifications
- **hotel_images**: Gallery images
- **hotel_rules**: Hotel policies

### Key Relationships
- Users can have multiple bookings
- Rooms can have multiple bookings, availabilities, and seasonal rates
- Bookings are linked to users (guests) and rooms
- Payments are linked to bookings

## 🎨 UI/UX Improvements

### Design System
- **Color Palette**: Professional blue and gold theme
- **Typography**: Clean, readable fonts
- **Spacing**: Consistent spacing system
- **Components**: Reusable UI components
- **Responsive**: Mobile-first design approach

### User Experience
- **Intuitive Navigation**: Clear navigation structure
- **Loading States**: Proper loading indicators
- **Error Handling**: User-friendly error messages
- **Form Validation**: Real-time validation feedback
- **Accessibility**: WCAG compliant design

## 🔧 Key Improvements Made

### 1. Database Structure
- ✅ Fixed missing user role and contact fields
- ✅ Removed duplicate migrations
- ✅ Proper foreign key relationships
- ✅ Comprehensive seeding data

### 2. Real Data Integration
- ✅ Replaced all hardcoded data with database queries
- ✅ Dynamic room listings with real availability
- ✅ Live booking statistics
- ✅ Real-time occupancy calculations

### 3. Booking Flow
- ✅ Complete booking form with validation
- ✅ Date selection and availability checking
- ✅ Guest information collection
- ✅ Booking confirmation system

### 4. Admin Dashboard
- ✅ Real-time statistics from database
- ✅ Live booking data
- ✅ Upcoming check-ins/check-outs
- ✅ Revenue tracking

### 5. Room Management
- ✅ Dynamic room filtering
- ✅ Real-time availability
- ✅ Comprehensive room details
- ✅ Related room suggestions

## 🚀 Usage

### For Guests
1. Visit the homepage to browse rooms
2. Use filters to find suitable accommodations
3. Click on a room to view details
4. Select dates and guest information
5. Complete the booking process
6. Receive confirmation with booking details

### For Administrators
1. Login with admin credentials
2. Access the admin dashboard for overview
3. Manage bookings through the booking management interface
4. Update room inventory and pricing
5. Handle emergency cases and guest requests
6. Monitor revenue and occupancy statistics

## 🔐 Authentication

### Default Admin Account
- **Email**: admin@hotel.com
- **Password**: password

### Default Guest Accounts
- **Email**: john.doe@example.com
- **Password**: password
- **Email**: jane.smith@example.com
- **Password**: password

## 📱 API Endpoints

### Booking API
- `GET /api/bookings` - List all bookings
- `GET /api/bookings/available-rooms` - Get available rooms for dates
- `GET /api/bookings/guests` - List all guests
- `GET /api/bookings/rooms` - List all rooms
- `GET /api/bookings/room-types` - Get room types
- `GET /api/bookings/stats` - Get booking statistics

### Room Management
- `GET /rooms` - List all rooms
- `POST /rooms` - Create new room
- `PUT /rooms/{id}` - Update room
- `DELETE /rooms/{id}` - Delete room

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📦 Deployment

### Production Build
```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Variables
Ensure all production environment variables are set:
- Database credentials
- Mail configuration
- Payment gateway settings
- File storage settings

## 🔧 Configuration

### Mail Configuration
Configure email settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourhotel.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Payment Gateway
Configure payment settings in `.env`:
```env
PAYHERE_MERCHANT_ID=your-merchant-id
PAYHERE_SECRET=your-secret-key
PAYHERE_SANDBOX=true
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Email: support@yourhotel.com
- Phone: +1 (555) 123-4567
- Documentation: [Link to documentation]

## 🔄 Changelog

### Version 1.0.0
- Initial release
- Complete booking system
- Admin dashboard
- Room management
- User authentication
- Emergency handling
- Gallery system

---

**Built with ❤️ using Laravel and Vue.js**
