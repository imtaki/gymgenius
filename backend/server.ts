import express from "express";
import cors from "cors";
import dotenv from "dotenv";
import router from "./Routes/authRoutes";

const app = express();
const PORT = process.env.PORT || 3001;


dotenv.config();

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(cors({ origin: "http://localhost:3000", credentials: true }));

app.get("/", (req, res) => {
  res.send("Hello from the backend!");
});

app.use("/api/auth", router);

// Start the server
app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});

