
import {
  Award,
  Bell,
  Search,
  User,
} from "lucide-react";

import {
  Link,
  useNavigate,
} from "react-router-dom";

import {
  useEffect,
  useState,
} from "react";

import "../css/navbar.css";

function Navbar() {
  const navigate = useNavigate();

  // token state
  const [token, setToken] = useState(
    localStorage.getItem("token")
  );

  // listen token change
  useEffect(() => {
    const updateToken = () => {
      setToken(localStorage.getItem("token"));
    };

    window.addEventListener(
      "tokenChanged",
      updateToken
    );

    return () => {
      window.removeEventListener(
        "tokenChanged",
        updateToken
      );
    };
  }, []);

  const handleProfileClick = () => {
    navigate("/profile");
  };

  return (
    <nav className="navbar">
      <div className="navbar-top">
        <div className="navbar-top-inner">

          <span className="navbar-tagline">
            Start learning today
          </span>

          <div className="navbar-top-actions">

            {!token ? (
              <>
                <Link to="/PageLogin">
                  <button className="btn btn-outline-sm">
                    Login
                  </button>
                </Link>

                <Link to="/PageRegister">
                  <button className="btn btn-outline-sm">
                    Register
                  </button>
                </Link>
              </>
            ) : (
              <button
                className="profile-btn"
                onClick={handleProfileClick}
              >
                <User size={20} />
              </button>
            )}

          </div>
        </div>
      </div>

      <div className="navbar-main">
        <div className="navbar-main-inner">

          <div className="logo">
            <div className="logo-icon">
              <Award size={22} />
            </div>

            <span className="logo-text">
              DRC
            </span>
          </div>

          <ul className="nav-links">
            <li>
              <Link to="/">Home</Link>

              <Link to="/courses">
                Courses
              </Link>

              <Link to="/categories">
                Categories
              </Link>
            </li>
          </ul>

          <div className="navbar-right">
            <button className="icon-btn">
              <Search size={18} />
            </button>

            <button className="icon-btn">
              <Bell size={18} />
            </button>
          </div>

        </div>
      </div>
    </nav>
  );
}

export default Navbar;

