import { Award, Bell, Search } from "lucide-react";
import { Link } from "react-router-dom";

function Navbar() {
  return (
    <nav className="navbar">
      <div className="navbar-top">
        <div className="navbar-top-inner">
          <span className="navbar-tagline">
            Start learning today - 250k+ students trust us
          </span>

          <div className="navbar-top-actions">

            <Link to="/login">
              <button className="btn btn-outline-sm">
                Login
              </button>
            </Link>

            <button className="btn btn-primary-sm">
              Register
            </button>
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
                <a
                  href="#"
                  className={`nav-link ${item === 'Home' ? 'active' : ''}`}
                >
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
  );
}

export default Navbar;