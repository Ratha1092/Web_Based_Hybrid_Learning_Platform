import { Routes, Route , Link } from "react-router-dom";

import Navbar from "./component/nabar";
import Hero from "./component/hero";
import Categories from "./component/categories";
import Footer from "./component/footer";
import Login from "./component/login";

function MainPage() {
  return (
    <>
      <Navbar />
      <Hero />
      <Categories />
      <Footer />
    </>
  );
}

function Home() {
  return (
    <Routes>
      <Route path="/" element={<MainPage />} />
      <Route path="/login" element={<Login />} />
    </Routes>
  );
}

export default Home;