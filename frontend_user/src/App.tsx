import './App.css'
import {Award, Bell, BookOpen, Camera, CheckCircle2, ChevronRight, Code2, DollarSign, FlaskConical, MessageSquare, PenTool, Play, Send, Search, Share2, Star, TrendingUp, Users, Video,Wifi, CirclePlay,} from 'lucide-react'

const categories = [
  { icon: PenTool, label: 'Art & Design', count: 38 },
  { icon: Code2, label: 'Development', count: 38 },
  { icon: MessageSquare, label: 'Communication', count: 30 },
  { icon: Video, label: 'Videography', count: 40 },
  { icon: Camera, label: 'Photography', count: 16 },
  { icon: TrendingUp, label: 'Marketing', count: 28 },
  { icon: BookOpen, label: 'Content Writing', count: 20 },
  { icon: DollarSign, label: 'Finance', count: 28 },
  { icon: FlaskConical, label: 'Science', count: 28 },
  { icon: Wifi, label: 'Network', count: 20 },
]

const courses = [
  {
    id: 1,
    tag: 'Photoshop',
    tagColor: '#ff6b35',
    title: 'Create An LMS Website With LearnPress',
    instructor: 'Determined Poster',
    weeks: 3,
    students: 156,
    rating: 4.5,
    reviews: 382,
    price: 'Free',
    image:
      'https://images.unsplash.com/photo-1593720219128-218e13fba49c?w=400&q=80',
  },
  {
    id: 2,
    tag: 'Development',
    tagColor: '#00bfa5',
    title: 'Design A Website With ThimPress',
    instructor: 'Determined Poster',
    weeks: 4,
    students: 96,
    rating: 4.8,
    reviews: 312,
    price: '$23.5',
    image:
      'https://images.unsplash.com/photo-1547658719-da2b51169166?w=400&q=80',
  },
  {
    id: 3,
    tag: 'Content Writing',
    tagColor: '#8bc34a',
    title: 'Create An LMS Website With LearnPress',
    instructor: 'Determined Poster',
    weeks: 3,
    students: 156,
    rating: 4.3,
    reviews: 204,
    price: '$32.5',
    image:
      'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=400&q=80',
  },
  {
    id: 4,
    tag: 'Communication',
    tagColor: '#9c27b0',
    title: 'Create An LMS Website With LearnPress',
    instructor: 'Determined Poster',
    weeks: 3,
    students: 156,
    rating: 4.5,
    reviews: 156,
    price: 'Free',
    image:
      'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&q=80',
  },
  {
    id: 5,
    tag: 'Videography',
    tagColor: '#ff5722',
    title: 'Create An LMS Website With LearnPress',
    instructor: 'Determined Poster',
    weeks: 3,
    students: 156,
    rating: 4.6,
    reviews: 198,
    price: 'Free',
    image:
      'https://images.unsplash.com/photo-1492619375914-88005aa9e8fb?w=400&q=80',
  },
  {
    id: 6,
    tag: 'Finance',
    tagColor: '#2196f3',
    title: 'Create An LMS Website With LearnPress',
    instructor: 'Determined Poster',
    weeks: 3,
    students: 156,
    rating: 4.4,
    reviews: 241,
    price: 'Free',
    image:
      'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=400&q=80',
  },
]

function StarRating({ rating }: { rating: number }) {
  return (
    <span className="star-rating">
      {[1, 2, 3, 4, 5].map((star) => (
        <Star
          key={star}
          size={12}
          fill={star <= Math.round(rating) ? '#ffc107' : 'none'}
          stroke={star <= Math.round(rating) ? '#ffc107' : '#c7c7cc'}
        />
      ))}
    </span>
  )
}

function Navbar() {
  return (
    <nav className="navbar">
      <div className="navbar-top">
        <div className="navbar-top-inner">
          <span className="navbar-tagline">
            Start learning today - 250k+ students trust us
          </span>
          <div className="navbar-top-actions">
            <button className="btn btn-outline-sm">Login</button>
            <button className="btn btn-primary-sm">Register</button>
          </div>
        </div>
      </div>
      <div className="navbar-main">
        <div className="navbar-main-inner">
          <div className="logo">
            <div className="logo-icon">
              <Award size={22} />
            </div>
            <span className="logo-text">DRC</span>
          </div>
          <ul className="nav-links">
            {['Home', 'Courses', 'Library', 'Contact'].map((item) => (
              <li key={item}>
                <a href="#" className={`nav-link ${item === 'Home' ? 'active' : ''}`}>
                  {item}
                </a>
              </li>
            ))}
          </ul>
          <div className="navbar-right">
            <button className="icon-btn" aria-label="Search">
              <Search size={18} />
            </button>
            <button className="icon-btn" aria-label="Notifications">
              <Bell size={18} />
            </button>
          </div>
        </div>
      </div>
    </nav>
  )
}

function Hero() {
  return (
    <section className="hero">
      <div className="hero-bg" />
      <div className="hero-inner">
        <div className="hero-content">
          <div className="hero-badge">
            <span className="badge-dot" />
            Online Learning Platform
          </div>
          <h1 className="hero-title">
            <span className="hero-highlight">Studying</span> Online is now
            <br />
            much easier
          </h1>
          <p className="hero-desc">
            TOTC is an interesting platform that will teach you in a more
            interactive way.
          </p>
          <div className="hero-actions">
            <button className="btn btn-white">Join for free</button>
            <button className="btn btn-ghost-white">
              <span className="play-icon">
                <Play size={14} fill="white" />
              </span>
              Watch how it works
            </button>
          </div>
          <div className="floating-stat">
            <div className="floating-stat-icon">
              <Users size={20} />
            </div>
            <div>
              <div className="floating-stat-value">250k+</div>
              <div className="floating-stat-label">Assisted Students</div>
            </div>
          </div>
        </div>

        <div className="hero-image-wrap">
          <div className="hero-img-backdrop" />
          <img
            src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=500&q=80"
            alt="Student learning online"
            className="hero-img"
          />

          <div className="floating-course-card">
            <div className="fcc-thumb">
              <BookOpen size={18} />
            </div>
            <div className="fcc-info">
              <div className="fcc-title">User Experience Class</div>
              <div className="fcc-meta">Tutor: 120,674</div>
            </div>
            <button className="fcc-btn">Join Now</button>
          </div>

          <div className="floating-congrats-card">
            <CheckCircle2 size={22} className="congrats-icon" />
            <div>
              <div className="congrats-title">Congratulations</div>
              <div className="congrats-sub">Your admission completed</div>
            </div>
          </div>
        </div>
      </div>

      <div className="hero-wave">
        <svg viewBox="0 0 1440 80" preserveAspectRatio="none" aria-hidden="true">
          <path d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z" fill="white" />
        </svg>
      </div>
    </section>
  )
}

function Categories() {
  return (
    <section className="section categories-section">
      <div className="container">
        <div className="section-header">
          <div>
            <h2 className="section-title">Top Categories</h2>
            <p className="section-sub">Explore our Popular Categories</p>
          </div>
          <button className="btn btn-outline">
            All Categories <ChevronRight size={16} />
          </button>
        </div>
        <div className="categories-grid">
          {categories.map((category) => {
            const Icon = category.icon

            return (
              <div className="category-card" key={category.label}>
                <div className="cat-icon-wrap">
                  <Icon size={26} />
                </div>
                <div className="cat-label">{category.label}</div>
                <div className="cat-count">{category.count} Courses</div>
              </div>
            )
          })}
        </div>
      </div>
    </section>
  )
}

function FeaturedCourses() {
  return (
    <section className="section courses-section">
      <div className="container">
        <div className="section-header">
          <div>
            <h2 className="section-title">Featured Courses</h2>
            <p className="section-sub">Explore our Popular Courses</p>
          </div>
          <button className="btn btn-outline">
            All Courses <ChevronRight size={16} />
          </button>
        </div>
        <div className="courses-grid">
          {courses.map((course) => (
            <div className="course-card" key={course.id}>
              <div className="course-thumb">
                <img src={course.image} alt={course.title} />
                <span className="course-tag" style={{ background: course.tagColor }}>
                  {course.tag}
                </span>
              </div>
              <div className="course-body">
                <div className="course-instructor">
                  <span className="instructor-dot" />
                  {course.instructor}
                </div>
                <h3 className="course-title">{course.title}</h3>
                <div className="course-meta">
                  <span>
                    <BookOpen size={12} /> {course.weeks} Weeks
                  </span>
                  <span>
                    <Users size={12} /> {course.students}+ Students
                  </span>
                </div>
                <div className="course-footer">
                  <div className="course-rating">
                    <StarRating rating={course.rating} />
                    <span className="rating-count">({course.reviews})</span>
                  </div>
                  <div className="course-bottom">
                    <span className={`course-price ${course.price === 'Free' ? 'free' : ''}`}>
                      {course.price}
                    </span>
                    <button className="view-more-btn">View More</button>
                  </div>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

function Footer() {
  return (
    <footer className="footer">
      <div className="container">
        <div className="footer-grid">
          <div className="footer-brand">
            <div className="logo footer-logo">
              <div className="logo-icon">
                <Award size={22} />
              </div>
              <span className="logo-text">DRC</span>
            </div>
            <p className="footer-desc">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
              eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <div className="social-links">
              <a href="#" className="social-btn" aria-label="Facebook">
                <Share2 size={16} />
              </a>
              <a href="#" className="social-btn" aria-label="Twitter">
                <Send size={16} />
              </a>
              <a href="#" className="social-btn" aria-label="Youtube">
                <CirclePlay size={16} />
              </a>
            </div>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">GET HELP</h4>
            <ul className="footer-links">
              {['Contact Us', 'Latest Articles', 'FAQ'].map((link) => (
                <li key={link}>
                  <a href="#">{link}</a>
                </li>
              ))}
            </ul>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">PROGRAMS</h4>
            <ul className="footer-links">
              {['Art & Design', 'Business', 'IT & Software', 'Languages', 'Programming'].map(
                (link) => (
                  <li key={link}>
                    <a href="#">{link}</a>
                  </li>
                ),
              )}
            </ul>
          </div>

          <div className="footer-col">
            <h4 className="footer-heading">CONTACT US</h4>
            <ul className="footer-links contact-list">
              <li>Address: 8 Charter Street, Bldg 1295, Natalie 2219 Tower</li>
              <li>Tel: +923 11 1234567</li>
              <li>Mail: admin@yoursite.com</li>
            </ul>
          </div>
        </div>
        <div className="footer-bottom">
          <p>Copyright 2024 DRC | Powered by DRC</p>
        </div>
      </div>
    </footer>
  )
}

export default function App() {
  return (
    <div className="app">
      <Navbar />
      <Hero />
      <Categories />
      <FeaturedCourses />
      <Footer />
    </div>
  )
}
