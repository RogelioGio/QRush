import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'
import './index.css'
import { BrowserRouter, RouterProvider } from "react-router-dom";
import App from './App.jsx'
import router from './router.jsx';
import { Toaster, toast } from 'sonner'
import { StateProvider } from './contexts/StateContext.jsx';


createRoot(document.getElementById('root')).render(
  <StrictMode>
      <StateProvider>
        <Toaster position='top-right'/>
        <RouterProvider router={router} />
      </StateProvider>     
  </StrictMode>,
)
