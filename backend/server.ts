import express from "express";
import cors from "cors";
import dotenv from "dotenv";
import router from "./Routes/authRoutes";
import cookieParser from "cookie-parser";
import { requireAdmin } from "./Middleware/auth";

const app = express();
const PORT = process.env.PORT || 3001;


dotenv.config();

app.use(express.json());
app.use(cookieParser());
app.use(express.urlencoded({ extended: true }));
app.use(cors({ origin: "http://localhost:3000", credentials: true }));

// Middleware to protect routes
app.get("/admin", requireAdmin, (req, res) => {
  res.json({ message: `Welcome Admin` });
});



app.get("/", (req, res) => {
  res.send("Hello from the backend!");
});
// Auth routes
app.use("/api/auth", router);


// Start the server
app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});

